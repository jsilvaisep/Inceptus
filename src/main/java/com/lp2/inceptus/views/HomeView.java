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
