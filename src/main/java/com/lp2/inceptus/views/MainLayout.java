package com.lp2.inceptus.views;

import com.vaadin.flow.component.Component;
import com.vaadin.flow.component.applayout.AppLayout;
import com.vaadin.flow.component.html.Anchor;
import com.vaadin.flow.component.html.Footer;
import com.vaadin.flow.component.html.Image;
import com.vaadin.flow.component.orderedlayout.FlexComponent;
import com.vaadin.flow.component.orderedlayout.HorizontalLayout;
import com.vaadin.flow.component.orderedlayout.VerticalLayout;
import com.vaadin.flow.router.RouterLayout;

public class MainLayout extends AppLayout implements RouterLayout {

    // Container para o conteúdo das views
    private VerticalLayout viewContainer = new VerticalLayout();

    public MainLayout() {
        // Cria o header
        HorizontalLayout header = createHeader();
        addToNavbar(header);

        // Configura o container onde o conteúdo (view) será inserido
        viewContainer.setId("main-content");
        viewContainer.setSizeFull();
        viewContainer.setPadding(false);
        viewContainer.setSpacing(false);
        viewContainer.setMargin(false);

        // Cria o footer
        Footer footer = createFooter();

        // Compor o layout principal (view + footer)
        VerticalLayout mainLayout = new VerticalLayout(viewContainer, footer);
        mainLayout.setId("main-wrapper");
        mainLayout.setSizeFull();
        mainLayout.setPadding(false);
        mainLayout.setSpacing(false);
        mainLayout.setMargin(false);
        mainLayout.setFlexGrow(1, viewContainer);

        setContent(mainLayout);
    }

    public void showRouterLayoutContent(Component content) {
        // Limpa e adiciona o conteúdo no container reservado
        viewContainer.removeAll();
        viewContainer.add(content);
    }

    private HorizontalLayout createHeader() {
        // Caminho para o logótipo; coloque-o na pasta "frontend/images" (recomendado)
        Image logo = new Image("images/logo.png", "Logo");
        logo.setHeight("40px");

        Anchor loginLink = new Anchor("/login", "Login");
        loginLink.getStyle().set("margin-left", "auto").set("color", "white");

        HorizontalLayout headerLayout = new HorizontalLayout(logo, loginLink);
        headerLayout.setWidthFull();
        headerLayout.setAlignItems(FlexComponent.Alignment.CENTER);
        headerLayout.getStyle()
                .set("padding", "1rem")
                .set("background-color", "#0E1A40");

        return headerLayout;
    }

    private Footer createFooter() {
        HorizontalLayout footerLayout = new HorizontalLayout(
                new Anchor("/sobre", "Sobre Nós"),
                new Anchor("/contactos", "Contactos"),
                new Anchor("/privacidade", "Privacidade")
        );
        footerLayout.setWidthFull();
        footerLayout.setJustifyContentMode(FlexComponent.JustifyContentMode.CENTER);
        footerLayout.getStyle()
                .set("background-color", "#0E1A40")
                .set("color", "white")
                .set("padding", "1rem");

        Footer footer = new Footer(footerLayout);
        footer.setWidthFull();
        return footer;
    }
}
