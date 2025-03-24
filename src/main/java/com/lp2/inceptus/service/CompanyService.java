package com.lp2.inceptus.service;

import com.lp2.inceptus.entity.Company;
import com.lp2.inceptus.repository.CompanyRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.List;

@Service
public class CompanyService {
    private final CompanyRepository companyRepository;

    public CompanyService(CompanyRepository companyRepository) {
        this.companyRepository = companyRepository;
    }

    public List<Company> findAll() {
        return companyRepository.findAll();
    }
    public List<Company> findTop() {
        return companyRepository.findTop10CompaniesNative();
    }
}
