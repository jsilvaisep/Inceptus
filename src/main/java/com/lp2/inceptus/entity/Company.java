package com.lp2.inceptus.entity;

import jakarta.persistence.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "COMPANY")
public class Company {

    @Id
    @Column(name = "COMPANY_ID", nullable = false)
    private Integer companyId;

    @Column(name = "COMPANY_NAME", insertable = false, updatable = false)
    private String companyName;

    @Column(name = "COMPANY_DESCRIPTION", insertable = false, updatable = false)
    private String companyDescription;

    @Column(name = "COMPANY_EMAIL", insertable = false, updatable = false)
    private String companyEmail;

    @Column(name = "COMPANY_PASSWORD", insertable = false, updatable = false)
    private String companyPassword;

    @Column(name = "COMPANY_SITE", insertable = false, updatable = false)
    private String companySite;

    @Column(name = "COMPANY_STATUS", insertable = false, updatable = false)
    private String companyStatus;

    @Column(name = "COMPANY_RANK", insertable = false, updatable = false)
    private Integer companyRank;

    @Column(name = "COMPANY_VIEW_QTY", insertable = false, updatable = false)
    private Integer companyViewQty;

    // Relacionamento com USER_TYPE (se realmente "TYPE_ID" em COMPANY aponta para USER_TYPE)
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "TYPE_ID", insertable = false, updatable = false)
    private UserType type;

    @Column(name = "IMG_URL", insertable = false, updatable = false)
    private String imgUrl;

    @Column(name = "CREATED_AT", insertable = false, updatable = false)
    private LocalDateTime createdAt;

    @Column(name = "UPDATED_AT", insertable = false, updatable = false)
    private LocalDateTime updatedAt;

    public Company() {}

    public Integer getCompanyId() {
        return companyId;
    }

    public String getCompanyName() {
        return companyName;
    }

    public String getCompanyDescription() {
        return companyDescription;
    }

    public String getCompanyEmail() {
        return companyEmail;
    }

    public String getCompanyPassword() {
        return companyPassword;
    }

    public String getCompanySite() {
        return companySite;
    }

    public String getCompanyStatus() {
        return companyStatus;
    }

    public Integer getCompanyRank() {
        return companyRank;
    }

    public Integer getCompanyViewQty() {
        return companyViewQty;
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