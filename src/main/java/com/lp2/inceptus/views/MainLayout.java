package com.lp2.inceptus.views;

import com.vaadin.flow.component.Component;
import com.vaadin.flow.component.applayout.AppLayout;
import com.vaadin.flow.component.html.Anchor;
import com.vaadin.flow.component.html.Footer;
import com.vaadin.flow.component.html.H1;
import com.vaadin.flow.component.html.Image;
import com.vaadin.flow.component.orderedlayout.FlexComponent;
import com.vaadin.flow.component.orderedlayout.HorizontalLayout;
import com.vaadin.flow.component.orderedlayout.VerticalLayout;
import com.vaadin.flow.router.RouterLayout;
import com.vaadin.flow.component.html.Anchor;
import com.vaadin.flow.component.html.Div;
import com.vaadin.flow.component.html.H1;
import com.vaadin.flow.component.orderedlayout.HorizontalLayout;
import com.vaadin.flow.component.orderedlayout.VerticalLayout;
import com.vaadin.flow.router.RouterLayout;

public class MainLayout extends AppLayout implements RouterLayout {
    public MainLayout() {
        // Título ou logo
        H1 title = new H1("Minha Aplicação");

        // Navbar com links
        Anchor homeLink = new Anchor("/", "Home");
        Anchor adminLink = new Anchor("/admin", "Admin");
        Anchor aboutLink = new Anchor("/about", "Sobre");

        HorizontalLayout navbar = new HorizontalLayout(homeLink, adminLink, aboutLink);
        navbar.setSpacing(true);

        // Adiciona tudo ao layout principal
        // add(title);
    }

}
