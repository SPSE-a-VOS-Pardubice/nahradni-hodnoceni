import React from 'react'

import { BrowserRouter, Route, Routes } from "react-router-dom";
import DashboardPage from "./pages/DashboardPage";
import GForm from "./pages/GForm";
import HeaderComponent from './components/HeaderComponent';
import { IntlProvider } from 'react-intl';
// import Create from "./pages/Create";
// import Edit from "./pages/Edit";
// import Graf from "./pages/Graf";
// import Login from "./pages/Login";
// import New_Pass from "./pages/New_Pass";

function App() {
  
  // TODO zde se postarat o authentikaci

  const messages = {
    "exam.type.5.short": "OZ",
    "exam.type.N.short": "NH",

    "time.unknown": "čas nezadán",

    "classroom.short":    "uč.",
    "classroom.unknown":  "nezadána",

    "mark.new.unknown":   "nezadána",
    "mark.1":             "Výborné",
    "mark.2":             "Chvalitebné",
    "mark.3":             "Dobré",
    "mark.4":             "Dostačující",
    "mark.5":             "Nedostačující",
    "mark.remove":        "Nehodnotit",

    "filter.status.finished":         "Dokončené",
    "filter.status.unfinished":       "Nedokončené",
    "filter.type.nahradni_hodnoceni": "Náhradní hodnocení (NH)",
    "filter.type.opravna_zkouska":    "Opravné zkoušky (OZ)",
    "filter.success.successful":      "Úspěšně",
    "filter.success.failed":          "Neúspěšně",

    "sort.student":         "Žáka (A-Z)",
    "sort.student.reverse": "Žáka (Z-A)",
    "sort.teacher":         "Učitele (A-Z)",
    "sort.teacher.reverse": "Učitele (Z-A)",
    "sort.class":           "Třídy (1. - 4.)",
    "sort.class.reverse":   "Třídy (4. - 1.)",
    "sort.mark":            "Známky (1 - 5)",
    "sort.mark.reverse":    "Známky (5 - 1)"
  }

  return (
    <IntlProvider messages={messages} locale="cs" defaultLocale="cs">
      <HeaderComponent />

      <main>
        <BrowserRouter>
          <Routes>
            <Route index element={ <DashboardPage /> } />
            <Route path='/data/:coll/:id' element={ <GForm /> } />
            {
            /* <Route path='/addExamp' element={ <Create /> } />
            <Route path='/editExamp' element={ <Edit /> } />
            <Route path='/graf' element={ <Graf /> } />
            <Route path='/login' element={ <Login /> } />
            <Route path='/new_pass' element={ <New_Pass /> } /> */
            }
          </Routes>
        </BrowserRouter>
      </main>
    </IntlProvider>
  );
}

export default App;
