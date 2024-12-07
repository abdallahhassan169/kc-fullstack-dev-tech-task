import "./App.css";
import CategoryNav from "./components/CategoryNav";
import "bootstrap/dist/css/bootstrap.min.css";
import React from "react";
import { Container, Navbar } from "react-bootstrap";

const App = () => {
  return (
    <div>
      <Container>
        <CategoryNav />
      </Container>
    </div>
  );
};

export default App;
