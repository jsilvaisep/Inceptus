package com.lp2.inceptus.views;

import com.lp2.inceptus.entity.Company;
import com.lp2.inceptus.entity.Product;
import com.lp2.inceptus.service.CompanyService;
import com.lp2.inceptus.service.ProductService;
import com.vaadin.flow.component.grid.Grid;
import com.vaadin.flow.component.html.Div;
import com.vaadin.flow.component.html.H2;
import com.vaadin.flow.component.html.Paragraph;
import com.vaadin.flow.component.orderedlayout.HorizontalLayout;
import com.vaadin.flow.component.orderedlayout.VerticalLayout;
import com.vaadin.flow.router.PageTitle;
import com.vaadin.flow.router.Route;

@Route(value = "", layout = MainLayout.class)
@PageTitle("Início")
public class HomeView extends HorizontalLayout {

    public HomeView(CompanyService companyService, ProductService productService) {
        setSizeFull();
        setSpacing(true);
        setPadding(true);

        // Secção esquerda (60%) com grids
        VerticalLayout leftLayout = new VerticalLayout();
        leftLayout.setWidth("60%");
        leftLayout.setPadding(false);
        leftLayout.setSpacing(true);

        H2 companiesTitle = new H2("Top rated Companies");
        Grid<Company> companyGrid = new Grid<>(Company.class, false);
        companyGrid.addColumn(Company::getCompanyName).setHeader("Nome");
        companyGrid.addColumn(Company::getCompanyRank).setHeader("Ranking");
        companyGrid.setItems(companyService.findTop()); // futuramente: topN sorted
        companyGrid.setWidthFull();

        H2 productsTitle = new H2("Top rated Products");
        Grid<Product> productGrid = new Grid<>(Product.class, false);
        productGrid.addColumn(Product::getProductName).setHeader("Nome");
        productGrid.addColumn(Product::getProductDescription).setHeader("Descrição");
        productGrid.addColumn(Product::getProductRank).setHeader("Ranking");
        productGrid.setItems(productService.findAll()); // futuramente: topN sorted
        productGrid.setWidthFull();

        leftLayout.add(companiesTitle, companyGrid, productsTitle, productGrid);

        // Secção direita (40%) com feed estilo X
        VerticalLayout rightLayout = new VerticalLayout();
        rightLayout.setWidth("40%");
        rightLayout.setPadding(false);
        rightLayout.setSpacing(true);

        H2 feedTitle = new H2("Últimos Posts das Empresas");
        // MOCK até a tabela POST existir
        Div post1 = new Div(new Paragraph("📢 Post da empresa X - novidade no produto Y!"));
        Div post2 = new Div(new Paragraph("📢 Empresa Z atingiu o top 3! Obrigado pela confiança."));
        Div post3 = new Div(new Paragraph("📢 Atualização no site da empresa ABC já disponível."));

        rightLayout.add(feedTitle, post1, post2, post3);

        // Adiciona ao layout principal
        add(leftLayout, rightLayout);
    }
}