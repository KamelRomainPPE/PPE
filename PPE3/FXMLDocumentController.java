/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ppe3;

import java.sql.Connection;
import java.sql.Statement;
import java.sql.Driver; 
import java.io.IOException;
import java.net.URL;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.util.ResourceBundle;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.fxml.Initializable;
import javafx.scene.Node;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.scene.control.PasswordField;
import javafx.scene.control.TextField;
import javafx.stage.Stage;

/**
 *
 * @author simon
 */
public class FXMLDocumentController implements Initializable {
    
    @FXML
    private Button bouton;
    
    @FXML
    private TextField login;
    
    @FXML
    private PasswordField password;
    
    @FXML
    private void handleButtonAction(ActionEvent event) {
        String loginContenu = login.getText();
        String passwordContenu = password.getText();
        
        String url = "jdbc:mysql://localhost/gsb_valide";
        String log = "root";
        String mdp = "";
        Connection c;
        Statement st;
        
        try {
            Class.forName("com.mysql.jdbc.Driver");
            c = (Connection) DriverManager.getConnection(url, log, mdp);
            st = (Statement) c.createStatement();
            ResultSet result = st.executeQuery("SELECT count(*) as nbr  from visiteur where login='"+loginContenu+"' and mdp='"+passwordContenu+"'");
            
            while (result.next()) {
                String nbr = result.getString("nbr");
                
                if(nbr.equals("1")) {
                    Parent Menu = FXMLLoader.load(getClass().getResource("Menu.fxml"));
                    Scene Menu_scene = new Scene(Menu);
                    Stage stage = (Stage)((Node)event.getSource()).getScene().getWindow();
                    stage.setScene(Menu_scene);
                    stage.show();
                }
                else {
                    Parent Index = FXMLLoader.load(getClass().getResource("Index.fxml"));
                    Scene Index_scene = new Scene(Index);
                    Stage stage = (Stage)((Node)event.getSource()).getScene().getWindow();
                    stage.setScene(Index_scene);
                    stage.show();
                }
            }
        
        }
        catch(Exception e) {
            e.printStackTrace();
        }
        
    }
    
    @FXML 
    private void close(ActionEvent event){ 
    System.out.println("Vous avez quitté l'aplication !");
    System.exit(0); 
    }
    
    @FXML
    private void OuvrirLogin(ActionEvent event) throws IOException{
        Parent Login = FXMLLoader.load(getClass().getResource("Login.fxml"));
        Scene Login_scene = new Scene(Login);
        Stage stage = (Stage)((Node)event.getSource()).getScene().getWindow();
        stage.setScene(Login_scene);
        stage.show();  
    }

    @FXML
    private void OuvrirMenu(ActionEvent event) throws IOException{
        Parent Menu = FXMLLoader.load(getClass().getResource("Menu.fxml"));
        Scene Menu_scene = new Scene(Menu);
        Stage stage = (Stage)((Node)event.getSource()).getScene().getWindow();
        stage.setScene(Menu_scene);
        stage.show();  
    }
    
    @FXML
    private void OuvrirMedecins(ActionEvent event) throws IOException {
        Parent Praticiens = FXMLLoader.load(getClass().getResource("Praticiens.fxml"));
        Scene Praticiens_scene = new Scene(Praticiens);
        Stage stage = (Stage)((Node)event.getSource()).getScene().getWindow();
        stage.setScene(Praticiens_scene);
        stage.show();  
    }
    
    @FXML
    private void OuvrirMedicaments(ActionEvent event) throws IOException{
        Parent Medicaments = FXMLLoader.load(getClass().getResource("Medicaments.fxml"));
        Scene Medicaments_scene = new Scene(Medicaments);
        Stage stage = (Stage)((Node)event.getSource()).getScene().getWindow();
        stage.setScene(Medicaments_scene);
        stage.show();  
    }
    
    @FXML
    private void OuvrirVisiteurs(ActionEvent event) throws IOException{
        Parent Visiteurs = FXMLLoader.load(getClass().getResource("Visiteurs.fxml"));
        Scene Visiteurs_scene = new Scene(Visiteurs);
        Stage stage = (Stage)((Node)event.getSource()).getScene().getWindow();
        stage.setScene(Visiteurs_scene);
        stage.show();  
    }

    @Override
    public void initialize(URL url, ResourceBundle rb) {
        // TODO
    }    
    
}
