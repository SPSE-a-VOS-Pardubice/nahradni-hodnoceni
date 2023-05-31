import React from 'react'

import { BrowserRouter, Route, Routes } from "react-router-dom";
import Home from "./pages/Home";
import GForm from "./pages/GForm";
// import Create from "./pages/Create";
// import Edit from "./pages/Edit";
// import Graf from "./pages/Graf";
// import Login from "./pages/Login";
// import New_Pass from "./pages/New_Pass";

function App() {

  // TODO přidat stránku pro zobrazení dalších dat

  return (
    <BrowserRouter>
      <Routes>
        <Route index element={ <Home /> } />

        <Route path='/data/:coll/:id' element={ <GForm /> } />
        {/* <Route path='/addExamp' element={ <Create /> } />
        <Route path='/editExamp' element={ <Edit /> } /> */}
        {/* <Route path='/graf' element={ <Graf /> } /> */}

        {/* <Route path='/login' element={ <Login /> } />
        <Route path='/new_pass' element={ <New_Pass /> } /> */}

      </Routes>
    </BrowserRouter>
  );
}

export default App;
