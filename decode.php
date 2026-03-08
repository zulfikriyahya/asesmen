<?php

/**
 * Decoder untuk obfuscated PHP (goto + hex/octal strings)
 * v3: tambah reconstruct if/else dari dua goto path
 *
 * php decode.php
 * Output: application/controllers_decoded/ & application/models_decoded/
 */

require_once __DIR__ . '/vendor/autoload.php';

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use PhpParser\Error;

// ── Visitor: decode string hex/octal ─────────────────────────────────────────
class StringDecoder extends NodeVisitorAbstract
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Scalar\String_) {
            return new Node\Scalar\String_($node->value);
        }
    }
}

// ── Visitor: resolve Goto ─────────────────────────────────────────────────────
class GotoResolver extends NodeVisitorAbstract
{
    public function leaveNode(Node $node)
    {
        if (!($node instanceof Stmt\ClassMethod || $node instanceof Stmt\Function_)) {
            return null;
        }
        if (empty($node->stmts)) {
            return null;
        }
        $node->stmts = $this->resolveBlock($node->stmts);
        return $node;
    }

    // ── Label map ──────────────────────────────────────────────────────────────

    private function buildLabelMap(array $stmts): array
    {
        $labels = [];
        foreach ($stmts as $i => $stmt) {
            if ($stmt instanceof Stmt\Label) {
                $labels[$stmt->name->name] = $i;
            }
        }
        return $labels;
    }

    // ── Main block resolver ────────────────────────────────────────────────────

    private function resolveBlock(array $stmts): array
    {
        $labels  = $this->buildLabelMap($stmts);
        $result  = [];
        $visited = [];
        $pos     = 0;
        $count   = count($stmts);

        while ($pos < $count) {
            if (isset($visited[$pos])) break;
            $visited[$pos] = true;
            $stmt = $stmts[$pos];

            if ($stmt instanceof Stmt\Label) {
                $pos++;
                continue;
            }

            if ($stmt instanceof Stmt\Goto_) {
                $target = $stmt->name->name;
                if (isset($labels[$target])) {
                    $newPos = $labels[$target];
                    if (isset($visited[$newPos])) break;
                    $pos = $newPos;
                } else {
                    $pos++;
                }
                continue;
            }

            if ($stmt instanceof Stmt\If_) {
                $reconstructed = $this->tryReconstructIfElse($stmt, $stmts, $labels, $pos, $visited);
                if ($reconstructed !== null) {
                    [$ifElseStmt, $mergePos] = $reconstructed;
                    $result[] = $ifElseStmt;
                    $pos = $mergePos;
                    continue;
                }
                $stmt = $this->resolveIfInner($stmt, $labels);
                $stmt = $this->stripTrailingEscapeGoto($stmt, $labels);
                $result[] = $stmt;
                $pos++;
                continue;
            }

            $stmt = $this->resolveCompoundStmt($stmt);
            $result[] = $stmt;
            $pos++;
        }

        return $result;
    }

    private function resolveBlockInner(array $stmts, array $outerLabels): array
    {
        $labels  = $this->buildLabelMap($stmts);
        $result  = [];
        $visited = [];
        $pos     = 0;
        $count   = count($stmts);

        while ($pos < $count) {
            if (isset($visited[$pos])) break;
            $visited[$pos] = true;
            $stmt = $stmts[$pos];

            if ($stmt instanceof Stmt\Label) {
                $pos++;
                continue;
            }

            if ($stmt instanceof Stmt\Goto_) {
                $target = $stmt->name->name;
                if (isset($labels[$target])) {
                    $newPos = $labels[$target];
                    if (isset($visited[$newPos])) break;
                    $pos = $newPos;
                } else {
                    $result[] = $stmt;
                    break;
                }
                continue;
            }

            if ($stmt instanceof Stmt\If_) {
                $stmt = $this->resolveIfInner($stmt, $labels + $outerLabels);
                $result[] = $stmt;
                $pos++;
                continue;
            }

            $stmt = $this->resolveCompoundStmt($stmt);
            $result[] = $stmt;
            $pos++;
        }

        return $result;
    }

    // ── If/else reconstruction ─────────────────────────────────────────────────

    private function tryReconstructIfElse(
        Stmt\If_ $ifStmt,
        array $stmts,
        array $labels,
        int $ifPos,
        array &$visited
    ): ?array {
        // if-body harus berisi tepat satu goto ke label yang dikenal
        if (count($ifStmt->stmts) !== 1) return null;
        $bodyStmt = $ifStmt->stmts[0];
        if (!($bodyStmt instanceof Stmt\Goto_)) return null;
        $thenLabel = $bodyStmt->name->name;
        if (!isset($labels[$thenLabel])) return null;

        // Statement setelah if
        $afterIfPos = $ifPos + 1;
        if (!isset($stmts[$afterIfPos])) return null;
        $afterIfStmt = $stmts[$afterIfPos];

        // Tentukan else start pos
        $elseStartPos = $afterIfPos;
        if ($afterIfStmt instanceof Stmt\Goto_ && isset($labels[$afterIfStmt->name->name])) {
            $elseStartPos = $labels[$afterIfStmt->name->name];
        }

        // Hindari then dan else mulai dari pos yang sama
        if ($elseStartPos === $labels[$thenLabel]) return null;

        // Collect kedua branch
        [$thenStmts, $thenMerge] = $this->collectBranch($stmts, $labels, $labels[$thenLabel]);
        if ($thenMerge === null) return null;

        [$elseStmts, $elseMerge] = $this->collectBranch($stmts, $labels, $elseStartPos);
        if ($elseMerge === null) return null;

        // Validasi merge: harus sama, atau salah satu/kedua terminal
        $sameMerge     = ($thenMerge === $elseMerge && $thenMerge !== '__terminal__');
        $bothTerminal  = ($thenMerge === '__terminal__' && $elseMerge === '__terminal__');
        $mixedTerminal = (
            ($thenMerge === '__terminal__' && $elseMerge !== null) ||
            ($elseMerge === '__terminal__' && $thenMerge !== null)
        );

        if (!$sameMerge && !$bothTerminal && !$mixedTerminal) return null;

        // Mark visited
        $visited[$ifPos]      = true;
        $visited[$afterIfPos] = true;
        $this->markBranchVisited($stmts, $labels, $labels[$thenLabel], $visited);
        $this->markBranchVisited($stmts, $labels, $elseStartPos, $visited);

        // Tentukan merge pos
        if ($bothTerminal) {
            $mergePos = count($stmts);
        } elseif ($sameMerge && isset($labels[$thenMerge])) {
            $mergePos = $labels[$thenMerge];
        } elseif ($thenMerge !== '__terminal__' && isset($labels[$thenMerge])) {
            $mergePos = $labels[$thenMerge];
        } elseif ($elseMerge !== '__terminal__' && isset($labels[$elseMerge])) {
            $mergePos = $labels[$elseMerge];
        } else {
            $mergePos = count($stmts);
        }

        $elseNode = empty($elseStmts) ? null : new Stmt\Else_($elseStmts);
        $newIf = new Stmt\If_(
            $ifStmt->cond,
            ['stmts' => $thenStmts, 'elseifs' => [], 'else' => $elseNode],
            $ifStmt->getAttributes()
        );

        return [$newIf, $mergePos];
    }

    private function collectBranch(array $stmts, array $labels, int $startPos): array
    {
        $result  = [];
        $visited = [];
        $pos     = $startPos;
        $count   = count($stmts);

        while ($pos < $count) {
            if (isset($visited[$pos])) {
                return [$result, null]; // loop back = tidak handle
            }
            $visited[$pos] = true;
            $stmt = $stmts[$pos];

            if ($stmt instanceof Stmt\Label) {
                $pos++;
                continue;
            }

            if ($stmt instanceof Stmt\Goto_) {
                $target = $stmt->name->name;
                if (isset($labels[$target])) {
                    $newPos = $labels[$target];
                    if (isset($visited[$newPos])) {
                        return [$result, $target]; // merge point
                    }
                    $pos = $newPos;
                } else {
                    return [$result, $target]; // escape ke outer
                }
                continue;
            }

            $result[] = $stmt;

            if ($stmt instanceof Stmt\Return_ || $stmt instanceof Stmt\Throw_) {
                return [$result, '__terminal__'];
            }

            $pos++;
        }

        if (!empty($result)) {
            $last = end($result);
            if ($last instanceof Stmt\Return_ || $last instanceof Stmt\Throw_) {
                return [$result, '__terminal__'];
            }
        }

        return [$result, null];
    }

    private function markBranchVisited(array $stmts, array $labels, int $startPos, array &$visited): void
    {
        $pos   = $startPos;
        $seen  = [];
        $count = count($stmts);

        while ($pos < $count) {
            if (isset($seen[$pos])) break;
            $seen[$pos]    = true;
            $visited[$pos] = true;
            $stmt = $stmts[$pos];

            if ($stmt instanceof Stmt\Label) {
                $pos++;
                continue;
            }

            if ($stmt instanceof Stmt\Goto_) {
                $target = $stmt->name->name;
                if (isset($labels[$target]) && !isset($seen[$labels[$target]])) {
                    $pos = $labels[$target];
                } else {
                    break;
                }
                continue;
            }

            if ($stmt instanceof Stmt\Return_ || $stmt instanceof Stmt\Throw_) break;

            $pos++;
        }
    }

    // ── If inner resolver ──────────────────────────────────────────────────────

    private function resolveIfInner(Stmt\If_ $stmt, array $outerLabels): Stmt\If_
    {
        if (!empty($stmt->stmts)) {
            $stmt->stmts = $this->resolveBlockInner($stmt->stmts, $outerLabels);
        }
        foreach ($stmt->elseifs as $elseif) {
            if (!empty($elseif->stmts)) {
                $elseif->stmts = $this->resolveBlockInner($elseif->stmts, $outerLabels);
            }
        }
        if ($stmt->else !== null && !empty($stmt->else->stmts)) {
            $stmt->else->stmts = $this->resolveBlockInner($stmt->else->stmts, $outerLabels);
        }
        return $stmt;
    }

    private function stripTrailingEscapeGoto(Stmt\If_ $stmt, array $outerLabels): Stmt\If_
    {
        $strip = function (array $stmts) use ($outerLabels): array {
            if (empty($stmts)) return $stmts;
            $last = end($stmts);
            if ($last instanceof Stmt\Goto_ && isset($outerLabels[$last->name->name])) {
                array_pop($stmts);
            }
            return $stmts;
        };

        $stmt->stmts = $strip($stmt->stmts);
        foreach ($stmt->elseifs as $elseif) {
            $elseif->stmts = $strip($elseif->stmts);
        }
        if ($stmt->else !== null) {
            $stmt->else->stmts = $strip($stmt->else->stmts);
        }
        return $stmt;
    }

    // ── Compound statement resolver ────────────────────────────────────────────

    private function resolveCompoundStmt(Stmt $stmt): Stmt
    {
        if (
            $stmt instanceof Stmt\Foreach_
            || $stmt instanceof Stmt\While_
            || $stmt instanceof Stmt\Do_
            || $stmt instanceof Stmt\For_
        ) {
            if (!empty($stmt->stmts)) {
                $stmt->stmts = $this->resolveBlock($stmt->stmts);
            }
        } elseif ($stmt instanceof Stmt\Switch_) {
            foreach ($stmt->cases as $case) {
                if (!empty($case->stmts)) {
                    $case->stmts = $this->resolveBlock($case->stmts);
                }
            }
        } elseif ($stmt instanceof Stmt\TryCatch) {
            if (!empty($stmt->stmts)) {
                $stmt->stmts = $this->resolveBlock($stmt->stmts);
            }
            foreach ($stmt->catches as $catch) {
                if (!empty($catch->stmts)) {
                    $catch->stmts = $this->resolveBlock($catch->stmts);
                }
            }
            if ($stmt->finally !== null) {
                $stmt->finally->stmts = $this->resolveBlock($stmt->finally->stmts);
            }
        }
        return $stmt;
    }
}

// ── Main processor ─────────────────────────────────────────────────────────────
function decodeFile(string $inputPath, string $outputPath): void
{
    $code = file_get_contents($inputPath);

    $parser    = (new ParserFactory)->createForNewestSupportedVersion();
    $traverser = new NodeTraverser;
    $printer   = new PrettyPrinter(['shortArraySyntax' => true]);

    $traverser->addVisitor(new StringDecoder());
    $traverser->addVisitor(new GotoResolver());

    try {
        $ast         = $parser->parse($code);
        $modifiedAst = $traverser->traverse($ast);
        $newCode     = $printer->prettyPrintFile($modifiedAst);

        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($outputPath, $newCode);
        echo "  [OK] " . basename($inputPath) . "\n";
    } catch (Error $e) {
        echo "  [ERR] " . basename($inputPath) . " — " . $e->getMessage() . "\n";
    }
}

function processDir(string $inputDir, string $outputDir): void
{
    $files = glob($inputDir . '/*.php');

    if (empty($files)) {
        echo "  Tidak ada file .php di: $inputDir\n";
        return;
    }

    echo "\nProcessing: $inputDir\n";
    echo str_repeat('─', 50) . "\n";

    foreach ($files as $file) {
        $filename   = basename($file);
        $outputPath = $outputDir . '/' . $filename;
        decodeFile($file, $outputPath);
    }
}

// ── Entry point ────────────────────────────────────────────────────────────────
$targets = [
    'application/controllers' => 'application/controllers_decoded',
    'application/models'      => 'application/models_decoded',
];

foreach ($targets as $input => $output) {
    $inputDir  = __DIR__ . '/' . $input;
    $outputDir = __DIR__ . '/' . $output;

    if (!is_dir($inputDir)) {
        echo "\n[SKIP] Direktori tidak ditemukan: $inputDir\n";
        continue;
    }

    processDir($inputDir, $outputDir);
}

echo "\n✓ Selesai.\n";
echo "Output:\n";
echo "  application/controllers_decoded/\n";
echo "  application/models_decoded/\n";
