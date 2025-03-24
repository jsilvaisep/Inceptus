package com.lp2.inceptus.entity;

import jakarta.persistence.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "COMMENT")
public class Comment {

    @Id
    @Column(name = "COMMENT_ID", nullable = false)
    private Integer commentId;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "USER_ID", insertable = false, updatable = false)
    private User user;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "COMPANY_ID", insertable = false, updatable = false)
    private Company company;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "PRODUCT_ID", insertable = false, updatable = false)
    private Product product;

    @Column(name = "COMMENT_TEXT", insertable = false, updatable = false)
    private String commentText;

    @Column(name = "COMMENT_STATUS", insertable = false, updatable = false)
    private String commentStatus;

    @Column(name = "CREATED_AT", insertable = false, updatable = false)
    private LocalDateTime createdAt;

    @Column(name = "UPDATED_AT", insertable = false, updatable = false)
    private LocalDateTime updatedAt;

    public Comment() {}

    public Integer getCommentId() {
        return commentId;
    }

    public User getUser() {
        return user;
    }

    public Company getCompany() {
        return company;
    }

    public Product getProduct() {
        return product;
    }

    public String getCommentText() {
        return commentText;
    }

    public String getCommentStatus() {
        return commentStatus;
    }

    public LocalDateTime getCreatedAt() {
        return createdAt;
    }

    public LocalDateTime getUpdatedAt() {
        return updatedAt;
    }
}