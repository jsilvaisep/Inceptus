package com.lp2.inceptus.service;

import com.lp2.inceptus.entity.UserType;
import com.lp2.inceptus.repository.UserTypeRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.Optional;

@Service
@Transactional(readOnly = true)
public class UserTypeService {

    private final UserTypeRepository userTypeRepository;

    public UserTypeService(UserTypeRepository userTypeRepository) {
        this.userTypeRepository = userTypeRepository;
    }

    public List<UserType> findAll() {
        return userTypeRepository.findAll();
    }

    public Optional<UserType> findById(Long id) {
        return userTypeRepository.findById(id);
    }
}
