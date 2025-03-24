package com.lp2.inceptus.entity;

import jakarta.persistence.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "CATEGORY")
public class Category {

    @Id
    @Column(name = "CATEGORY_ID", nullable = false)
    private Integer categoryId;

    @Column(name = "CATEGORY_NAME", insertable = false, updatable = false)
    private String categoryName;

    @Column(name = "CATEGORY_TYPE", insertable = false, updatable = false)
    private String categoryType;

    @Column(name = "CREATED_AT", insertable = false, updatable = false)
    private LocalDateTime createdAt;

    @Column(name = "UPDATED_AT", insertable = false, updatable = false)
    private LocalDateTime updatedAt;

    public Category() {}

    public Integer getCategoryId() {
        return categoryId;
    }

    public String getCategoryName() {
        return categoryName;
    }

    public String getCategoryType() {
        return categoryType;
    }

    public LocalDateTime getCreatedAt() {
        return createdAt;
    }

    public LocalDateTime getUpdatedAt() {
        return updatedAt;
    }
}