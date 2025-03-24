package com.lp2.inceptus.service;

import com.lp2.inceptus.entity.Product;
import com.lp2.inceptus.repository.ProductRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.Optional;

@Service
public class ProductService {

    private final ProductRepository productRepository;

    public ProductService(ProductRepository productRepository) {
        this.productRepository = productRepository;
    }

    /**
     * Retorna todos os produtos da base de dados.
     */
    public List<Product> findAll() {
        return productRepository.findAll();
    }
}



