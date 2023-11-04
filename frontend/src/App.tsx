import {IntlProvider} from 'react-intl';
import {BrowserRouter, Route, Routes} from 'react-router-dom';
import Header from './components/Header';
import DashboardPage from './pages/Dashboard';
import {ExamsContextProvider} from './contexts/ExamsContext';
import {PeriodContextProvider} from './contexts/PeriodContext';
import ImportPage from './pages/ImportPage';
// import Create from "./pages/Create";
// import Edit from "./pages/Edit";
// import Graf from "./pages/Graf";
// import Login from "./pages/Login";
// import New_Pass from "./pages/New_Pass";

function App() {

  const messages = {
    'exam.type.NH.short': 'NH',
    'exam.type.OZ.short': 'OZ',

    'time.unknown': 'čas nezadán',

    'classroom.short': 'uč.',
    'classroom.unknown': 'nezadána',

    'mark.new.unknown': 'nezadána',
    'mark.1': 'Výborné',
    'mark.2': 'Chvalitebné',
    'mark.3': 'Dobré',
    'mark.4': 'Dostačující',
    'mark.5': 'Nedostačující',
    'mark.remove': 'Nehodnotit',

    'filter.status.finished': 'Dokončené',
    'filter.status.unfinished': 'Nedokončené',
    'filter.type.nahradni_hodnoceni': 'Náhradní hodnocení (NH)',
    'filter.type.opravna_zkouska': 'Opravné zkoušky (OZ)',
    'filter.success.successful': 'Úspěšně',
    'filter.success.failed': 'Neúspěšně',

    'sort.student': 'Žáka (A-Z)',
    'sort.student.reverse': 'Žáka (Z-A)',
    'sort.teacher': 'Učitele (A-Z)',
    'sort.teacher.reverse': 'Učitele (Z-A)',
    'sort.class': 'Třídy (1. - 4.)',
    'sort.class.reverse': 'Třídy (4. - 1.)',
    'sort.mark': 'Známky (1 - 5)',
    'sort.mark.reverse': 'Známky (5 - 1)',

    'import.drop': 'klikněte pro vybrání souboru, nebo ho sem přetáhněte',
    'import.uploading': 'probíhá nahrávání',
  };

  return (
    <IntlProvider messages={messages} locale="cs" defaultLocale="cs">
      <PeriodContextProvider>
        <ExamsContextProvider>
          <Header />
          <main>
            <BrowserRouter>
              <Routes>
                <Route index element={ <DashboardPage /> } />
                <Route path="/import" element={ <ImportPage /> } />

                {/* <Route path='/data/:coll/:id' element={ <GForm /> } />
                <Route path='/addExamp' element={ <Create /> } />
                <Route path='/editExamp' element={ <Edit /> } />
                <Route path='/graf' element={ <Graf /> } />
                <Route path='/login' element={ <Login /> } />
                <Route path='/new_pass' element={ <New_Pass /> } /> */
                }
              </Routes>
            </BrowserRouter>
          </main>
        </ExamsContextProvider>
      </PeriodContextProvider>
    </IntlProvider>
  );
}

export default App;
