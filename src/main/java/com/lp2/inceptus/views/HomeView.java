package com.lp2.inceptus.views;

import com.lp2.inceptus.entity.Company;
import com.lp2.inceptus.entity.Product;
import com.lp2.inceptus.service.CompanyService;
import com.lp2.inceptus.service.ProductService;
import com.vaadin.flow.component.grid.Grid;
import com.vaadin.flow.component.html.H1;
import com.vaadin.flow.component.html.H2;
import com.vaadin.flow.component.orderedlayout.HorizontalLayout;
import com.vaadin.flow.component.orderedlayout.VerticalLayout;
import com.vaadin.flow.router.PageTitle;
import com.vaadin.flow.router.Route;

@Route(value = "", layout = MainLayout.class)
@PageTitle("Início")
public class HomeView extends VerticalLayout {

    public HomeView(CompanyService companyService, ProductService productService) {
        setSizeFull();
        setPadding(true);
        setSpacing(true);
        setJustifyContentMode(JustifyContentMode.CENTER);
        setAlignItems(Alignment.CENTER);

        H1 welcome = new H1("Bem-vindo à Inceptus!");

        // Grid para Companies
        Grid<Company> companyGrid = new Grid<>(Company.class, false);
        // Coluna de teste (ID)
        companyGrid.addColumn(Company::getCompanyId).setHeader("ID");
        // Coluna de nome
        companyGrid.addColumn(Company::getCompanyName).setHeader("Empresa");
        companyGrid.setItems(companyService.findAll());

        // Grid para Products
        Grid<Product> productGrid = new Grid<>(Product.class, false);
        // Coluna de teste (ID)
        productGrid.addColumn(Product::getProductId).setHeader("ID");
        // Coluna de nome
        productGrid.addColumn(Product::getProductName).setHeader("Produto");
        productGrid.setItems(productService.findAll());

        // Títulos e Layouts
        VerticalLayout companiesLayout = new VerticalLayout(new H2("Empresas"), companyGrid);
        companiesLayout.setSizeFull();

        VerticalLayout productsLayout = new VerticalLayout(new H2("Produtos"), productGrid);
        productsLayout.setSizeFull();

        HorizontalLayout gridsLayout = new HorizontalLayout(companiesLayout, productsLayout);
        gridsLayout.setSizeFull();
        gridsLayout.setFlexGrow(1, companiesLayout, productsLayout);

        add(welcome, gridsLayout);

        // Debug
        int totalCompanies = companyService.findAll().size();
        int totalProducts = productService.findAll().size();
        System.out.println("Total de Companies: " + totalCompanies);
        System.out.println("Total de Products: " + totalProducts);
    }
}




//public class MainView extends VerticalLayout {
//
//    private Grid<User> userGrid = new Grid<>(User.class);
//    private UserRepository userRepository = new UserRepository();
//
//    public MainView(GreetService service) {
//
//        Button btnCarregar = new Button("Carregar Users", event -> carregarUsers());
//        add(btnCarregar, userGrid);
//
//        // por algum motivo o nome dentro do setcollums
//        // tem que ser igual aos atributos da classe
//        userGrid.setColumns("id", "name", "email");
//
//        // Use TextField for standard text input
//        TextField textField = new TextField("Your name");
//        textField.addClassName("bordered");
//
//        // Button click listeners can be defined as lambda expressions
//        Button button = new Button("Say hello", e -> {
//            add(new Paragraph(service.greet(textField.getValue())));
//        });
//
//        Button btnTestarConexao = new Button("Testar Conexão", e -> {
//            DatabaseConnection conn = new DatabaseConnection();
//            conn.testarConexao();
//        });
//        add(btnTestarConexao);
//
//        // Theme variants give you predefined extra styles for components.
//        // Example: Primary button has a more prominent look.
//        button.addThemeVariants(ButtonVariant.LUMO_PRIMARY);
//
//        // You can specify keyboard shortcuts for buttons.
//        // Example: Pressing enter in this view clicks the Button.
//        button.addClickShortcut(Key.ENTER);
//
//        // Use custom CSS classes to apply styling. This is defined in
//        // styles.css.
//        addClassName("centered-content");
//
//        add(textField, button, btnTestarConexao, btnCarregar, userGrid);
//    }
//
//    private void carregarUsers() {
//        try {
//            List<User> users = userRepository.getAllUsers();
//            userGrid.setItems(users);
//            Notification.show("Dados carregados com sucesso!");
//        } catch (Exception e) {
//            Notification.show("Erro ao carregar: " + e.getMessage());
//        }
//    }
//
//}
