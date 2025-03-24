package com.lp2.inceptus.repository;

import com.lp2.inceptus.entity.Post;
import org.springframework.data.jpa.repository.JpaRepository;
import java.util.List;

public interface PostRepository extends JpaRepository<Post, Integer> {
    List<Post> findAllByOrderByCreatedAtDesc();
}