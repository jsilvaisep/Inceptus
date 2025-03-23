package org.vaadin.example;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.util.ArrayList;
import java.util.List;

public class UserRepository {

    public List<User> getAllUsers() {
        List<User> users = new ArrayList<>();

        String sql = "SELECT USER_ID, USER_NAME, USER_EMAIL FROM USER";

        try (Connection conn = DatabaseConnection.getConnection();
             PreparedStatement stmt = conn.prepareStatement(sql);
             ResultSet rs = stmt.executeQuery()) {

            while (rs.next()) {
                users.add(new User(
                        rs.getInt("USER_ID"),
                        rs.getString("USER_NAME"),
                        rs.getString("USER_EMAIL")
                ));
            }

        } catch (Exception e) {
            e.printStackTrace(); // Ou logar
        }

        return users;
    }
}
