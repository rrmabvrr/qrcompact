import React from "react";
import { createRoot } from "react-dom/client";
import "../public/styles.css";
import PixApp from "./PixApp";

createRoot(document.getElementById("root")).render(
    <React.StrictMode>
        <PixApp />
    </React.StrictMode>
);
