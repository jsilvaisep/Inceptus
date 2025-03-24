package com.lp2.inceptus.entity;

import jakarta.persistence.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "USER_TYPE")
public class UserType {

    @Id
    @Column(name = "TYPE_ID", nullable = false)
    private Integer typeId;

    @Column(name = "USER_TYPE", insertable = false, updatable = false)
    private String userType;

    @Column(name = "CREATED_AT", insertable = false, updatable = false)
    private LocalDateTime createdAt;

    @Column(name = "UPDATED_AT", insertable = false, updatable = false)
    private LocalDateTime updatedAt;

    // Construtor vazio exigido pelo JPA
    public UserType() {}

    // Getters apenas (sem setters -> read-only)
    public Integer getTypeId() {
        return typeId;
    }

    public String getUserType() {
        return userType;
    }

    public LocalDateTime getCreatedAt() {
        return createdAt;
    }

    public LocalDateTime getUpdatedAt() {
        return updatedAt;
    }
}