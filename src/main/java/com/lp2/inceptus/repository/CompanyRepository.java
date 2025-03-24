package com.lp2.inceptus.repository;

import com.lp2.inceptus.entity.Company;
import org.springframework.data.jpa.repository.JpaRepository;

public interface CompanyRepository extends JpaRepository<Company, Long> {
}