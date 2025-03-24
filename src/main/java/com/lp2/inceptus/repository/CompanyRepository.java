package com.lp2.inceptus.repository;

import com.lp2.inceptus.entity.Company;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface CompanyRepository extends JpaRepository<Company, Long> {

    @Query(value = "call COMPANY_TOP()", nativeQuery = true)
    List<Company> findTop10CompaniesNative();
}