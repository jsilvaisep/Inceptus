package com.lp2.inceptus.repository;

import com.lp2.inceptus.entity.Category;
import org.springframework.data.jpa.repository.JpaRepository;

public interface CategoryRepository extends JpaRepository<Category, Long> {
}