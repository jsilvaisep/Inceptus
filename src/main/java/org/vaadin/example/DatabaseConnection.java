package org.vaadin.example;

import com.vaadin.flow.component.notification.Notification;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class DatabaseConnection {
        private static final String URL = "jdbc:mysql://143.47.56.69:3306/DB_INCEPTUS_PP";
        private static final String USER = "vaadin_user";
        private static final String PASSWORD = "1ri*45iYQ3";

        public static Connection getConnection() throws SQLException {
                return DriverManager.getConnection(URL, USER, PASSWORD);
        }

        public void testarConexao() {
                try (Connection conn = DatabaseConnection.getConnection()) {
                        if (conn != null && !conn.isClosed()) {
                                Notification.show("Conexão bem-sucedida com a Base de dados!");
                        } else {
                                Notification.show("Falha ao conectar a Base de dados!");
                        }
                } catch (Exception e) {
                        Notification.show("Erro: " + e.getMessage(), 5000, Notification.Position.MIDDLE);
                }
        }
}
