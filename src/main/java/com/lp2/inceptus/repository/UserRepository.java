package com.lp2.inceptus.repository;

import com.lp2.inceptus.entity.User;
import org.springframework.data.jpa.repository.JpaRepository;

//import java.sql.Connection;
//import java.sql.PreparedStatement;
//import java.sql.ResultSet;
//import java.util.ArrayList;
//import java.util.List;

public interface UserRepository extends JpaRepository<User, Long> {
    User findByUserEmail(String userEmail);

//    List<User> getAllUsers() {
//        List<User> users = new ArrayList<>();
//
//        String sql = "SELECT USER_ID, USER_NAME, USER_EMAIL FROM USER";
//
//        try (Connection conn = DatabaseConnection.getConnection();
//             PreparedStatement stmt = conn.prepareStatement(sql);
//             ResultSet rs = stmt.executeQuery()) {
//
//            while (rs.next()) {
//                users.add(new User(
//                        rs.getInt("USER_ID"),
//                        rs.getString("USER_NAME"),
//                        rs.getString("USER_EMAIL")
//                ));
//            }
//
//        } catch (Exception e) {
//            e.printStackTrace(); // Ou logar
//        }
//
//        return users;
//    }
}
