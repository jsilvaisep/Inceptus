package com.lp2.inceptus.repository;

import com.lp2.inceptus.entity.UserType;
import org.springframework.data.jpa.repository.JpaRepository;

public interface UserTypeRepository extends JpaRepository<UserType, Long> {
}