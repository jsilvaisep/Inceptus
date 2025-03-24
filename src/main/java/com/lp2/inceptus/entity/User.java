package com.lp2.inceptus.entity;

import jakarta.persistence.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "USER")
public class User {

    @Id
    @Column(name = "USER_ID", nullable = false)
    private Integer userId;

    @Column(name = "USER_NAME", insertable = false, updatable = false)
    private String userName;

    @Column(name = "USER_EMAIL", insertable = false, updatable = false)
    private String userEmail;

    @Column(name = "USER_PASSWORD", insertable = false, updatable = false)
    private String userPassword;

    @Column(name = "USER_STATUS", insertable = false, updatable = false)
    private String userStatus;  // ex.: 'A' (Active), 'I' (Inactive) etc.

    // Relacionamento com USER_TYPE (muitos usuários podem ter um mesmo tipo)
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "TYPE_ID", insertable = false, updatable = false)
    private UserType type;

    @Column(name = "IMG_URL", insertable = false, updatable = false)
    private String imgUrl;

    @Column(name = "CREATED_AT", insertable = false, updatable = false)
    private LocalDateTime createdAt;

    @Column(name = "UPDATED_AT", insertable = false, updatable = false)
    private LocalDateTime updatedAt;

    public User() {}

    public Integer getUserId() {
        return userId;
    }

    public String getUserName() {
        return userName;
    }

    public String getUserEmail() {
        return userEmail;
    }

    public String getUserPassword() {
        return userPassword;
    }

    public String getUserStatus() {
        return userStatus;
    }

    public UserType getType() {
        return type;
    }

    public String getImgUrl() {
        return imgUrl;
    }

    public LocalDateTime getCreatedAt() {
        return createdAt;
    }

    public LocalDateTime getUpdatedAt() {
        return updatedAt;
    }
}