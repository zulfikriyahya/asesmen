<?php

class Post_model extends CI_Model
{
    public function getPostUser($id_user)
    {
        $this->db->select('a.*, b.nama_guru, b.foto, (SELECT COUNT(post_comments.id_comment) FROM post_comments WHERE a.id_post = post_comments.id_post) AS jml');
        $this->db->from('post a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        if (!($id_user != 0)) {
            $this->db->order_by('a.updated', 'desc');
            return $this->db->get()->result();
        } else {
            $this->db->where('a.dari', $id_user);
            $this->db->order_by('a.updated', 'desc');
            return $this->db->get()->result();
        }
    }
    public function getPostForUser($kepada, $kelas = null)
    {
        $this->db->select('a.*, b.nama_guru, b.foto, (SELECT COUNT(post_comments.id_comment) FROM post_comments WHERE a.id_post = post_comments.id_post) AS jml');
        $this->db->from('post a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        if (!($kepada != null)) {
            $this->db->order_by('a.updated', 'desc');
            return $this->db->get()->result();
        } else {
            $this->db->where('(a.kepada LIKE ' . $kepada . ') OR (a.kepada LIKE ' . $kelas . ')');
            $this->db->order_by('a.updated', 'desc');
            return $this->db->get()->result();
        }
    }
    public function getIdComments($id_post)
    {
        $this->db->select('id_comment');
        $this->db->where('id_post', $id_post);
        return $this->db->get('post_comments')->result();
    }
    public function getIdReplies($id_comment)
    {
        $this->db->select('id_reply');
        if (is_array($id_comment)) {
            $this->db->where_in('id_comment', $id_comment);
            return $this->db->get('post_reply')->result();
        } else {
            $this->db->where('id_comment', $id_comment);
            return $this->db->get('post_reply')->result();
        }
    }
}