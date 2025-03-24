package com.lp2.inceptus.entity;

import jakarta.persistence.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "POST")
public class Post {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "POST_ID")
    private Integer postId;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "COMPANY_ID", nullable = false)
    private Company company;

    @Column(name = "POST_CONTENT", nullable = false, length = 2000)
    private String postContent;

    @Column(name = "CREATED_AT")
    private LocalDateTime createdAt;

    @Column(name = "UPDATED_AT")
    private LocalDateTime updatedAt;

    // Getters e Setters

    public Integer getPostId() {
        return postId;
    }

    public Company getCompany() {
        return company;
    }

    public String getPostContent() {
        return postContent;
    }

    public LocalDateTime getCreatedAt() {
        return createdAt;
    }

    public LocalDateTime getUpdatedAt() {
        return updatedAt;
    }

    public void setCompany(Company company) {
        this.company = company;
    }

    public void setPostContent(String postContent) {
        this.postContent = postContent;
    }

    public void setCreatedAt(LocalDateTime createdAt) {
        this.createdAt = createdAt;
    }

    public void setUpdatedAt(LocalDateTime updatedAt) {
        this.updatedAt = updatedAt;
    }
}