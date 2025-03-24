package com.lp2.inceptus.entity;

import jakarta.persistence.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "PRODUCT")
public class Product {

    @Id
    @Column(name = "PRODUCT_ID", nullable = false)
    private Integer productId;

    @Column(name = "PRODUCT_NAME", insertable = false, updatable = false)
    private String productName;

    @Column(name = "PRODUCT_DESCRIPTION", insertable = false, updatable = false)
    private String productDescription;

    // Categoria
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "CATEGORY_ID", insertable = false, updatable = false)
    private Category category;

    // Empresa
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "COMPANY_ID", insertable = false, updatable = false)
    private Company company;

    @Column(name = "PRODUCT_STATUS", insertable = false, updatable = false)
    private String productStatus;

    @Column(name = "PRODUCT_RANK", insertable = false, updatable = false)
    private Integer productRank;

    @Column(name = "PRODUCT_VIEW_QTY", insertable = false, updatable = false)
    private Integer productViewQty;

    @Column(name = "IMG_URL", insertable = false, updatable = false)
    private String imgUrl;

    @Column(name = "CREATED_AT", insertable = false, updatable = false)
    private LocalDateTime createdAt;

    @Column(name = "UPDATED_AT", insertable = false, updatable = false)
    private LocalDateTime updatedAt;

    public Product() {}

    public Integer getProductId() {
        return productId;
    }

    public String getProductName() {
        return productName;
    }

    public String getProductDescription() {
        return productDescription;
    }

    public Category getCategory() {
        return category;
    }

    public Company getCompany() {
        return company;
    }

    public String getProductStatus() {
        return productStatus;
    }

    public Integer getProductRank() {
        return productRank;
    }

    public Integer getProductViewQty() {
        return productViewQty;
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