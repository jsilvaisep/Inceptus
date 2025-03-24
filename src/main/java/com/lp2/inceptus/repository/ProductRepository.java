package com.lp2.inceptus.repository;

import com.lp2.inceptus.entity.Product;
import org.springframework.data.jpa.repository.JpaRepository;

public interface ProductRepository extends JpaRepository<Product, Long> {
}