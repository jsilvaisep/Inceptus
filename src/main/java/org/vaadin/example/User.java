package org.vaadin.example;

public class User {
    private int id;
    private String name;
    private String email;

    public User(int id, String name, String email) {
        this.id = id;
        this.name = name;
        this.email = email;
    }

    // Getters e setters
    public int getId() { return id; }
    public String getName() { return name; }
    public String getEmail() { return email; }
}
