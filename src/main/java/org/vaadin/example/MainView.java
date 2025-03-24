package org.vaadin.example;

import com.vaadin.flow.component.Key;
import com.vaadin.flow.component.button.Button;
import com.vaadin.flow.component.button.ButtonVariant;
import com.vaadin.flow.component.grid.Grid;
import com.vaadin.flow.component.html.Paragraph;
import com.vaadin.flow.component.notification.Notification;
import com.vaadin.flow.component.orderedlayout.VerticalLayout;
import com.vaadin.flow.component.textfield.TextField;
import com.vaadin.flow.router.Route;
import org.vaadin.example.model.User;
import org.vaadin.example.repository.UserRepository;

import java.util.List;

/**
 * A sample Vaadin view class.
 * <p>
 * To implement a Vaadin view just extend any Vaadin component and use @Route
 * annotation to announce it in a URL as a Spring managed bean.
 * <p>
 * A new instance of this class is created for every new user and every browser
 * tab/window.
 * <p>
 * The main view contains a text field for getting the user name and a button
 * that shows a greeting message in a notification.
 */
@Route
public class MainView extends VerticalLayout {

    private Grid<User> userGrid = new Grid<>(User.class);
    private UserRepository userRepository = new UserRepository();

    public MainView(GreetService service) {

        Button btnCarregar = new Button("Carregar Users", event -> carregarUsers());
        add(btnCarregar, userGrid);

        // por algum motivo o nome dentro do setcollums
        // tem que ser igual aos atributos da classe
        userGrid.setColumns("id", "name", "email");

        // Use TextField for standard text input
        TextField textField = new TextField("Your name");
        textField.addClassName("bordered");

        // Button click listeners can be defined as lambda expressions
        Button button = new Button("Say hello", e -> {
            add(new Paragraph(service.greet(textField.getValue())));
        });

        Button btnTestarConexao = new Button("Testar Conexão", e -> {
            DatabaseConnection conn = new DatabaseConnection();
            conn.testarConexao();
        });
        add(btnTestarConexao);

        // Theme variants give you predefined extra styles for components.
        // Example: Primary button has a more prominent look.
        button.addThemeVariants(ButtonVariant.LUMO_PRIMARY);

        // You can specify keyboard shortcuts for buttons.
        // Example: Pressing enter in this view clicks the Button.
        button.addClickShortcut(Key.ENTER);

        // Use custom CSS classes to apply styling. This is defined in
        // styles.css.
        addClassName("centered-content");

        add(textField, button, btnTestarConexao, btnCarregar, userGrid);
    }

    private void carregarUsers() {
        try {
            List<User> users = userRepository.getAllUsers();
            userGrid.setItems(users);
            Notification.show("Dados carregados com sucesso!");
        } catch (Exception e) {
            Notification.show("Erro ao carregar: " + e.getMessage());
        }
    }

}
