package com.lp2.inceptus.service;

import com.lp2.inceptus.entity.Post;
import com.lp2.inceptus.repository.PostRepository;
import org.springframework.stereotype.Service;
import java.util.List;

@Service
public class PostService {

    private final PostRepository postRepository;

    public PostService(PostRepository postRepository) {
        this.postRepository = postRepository;
    }

    public List<Post> getAllPostsSortedByDate() {
        return postRepository.findAllByOrderByCreatedAtDesc();
    }
}