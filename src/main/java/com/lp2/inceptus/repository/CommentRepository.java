package com.lp2.inceptus.repository;

import com.lp2.inceptus.entity.Comment;
import org.springframework.data.jpa.repository.JpaRepository;

public interface CommentRepository extends JpaRepository<Comment, Long> {
}