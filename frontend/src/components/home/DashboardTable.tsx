import {useContext} from 'react';
import './DashboardTable.css';
import DashboardTableItem from './DashboardTableItem';
import {ExamsContext} from '../../contexts/ExamsContext';
import DashboardSearch from './DashboardSearch';

const DashboardTable = () => {
  const examsContext = useContext(ExamsContext);
  // existence of exams is already checked in DashboardPage
  if (examsContext.id !== 'SUCCESS') {
    return <></>;
  }
  const exams = examsContext.content.data;

  const filteredExams = exams;

  return (
    <>
      <DashboardSearch />
      {/* <FilterOptions /> */}

      <div className="num_of_records_part">
        <p>Zobrazeno {exams.length} záznamů</p>
      </div>

      <table className="dashboard">
        <tbody>
          {filteredExams.map((exam, i) => (
            <DashboardTableItem key={i} exam={exam} />
          ))}
        </tbody>
      </table>

      <div className="delete_popup popup">
        <div className="popup_container">
          <p>
            Opravdu si přejete smazat náhradní hodnocení{' '}
            <span className="student_name"></span>
          </p>
          <div className="decision_row">
            <button id="yes_delete">ANO</button>
            <button id="no_delete">NE</button>
          </div>
        </div>
      </div>

      <div className="repair_popup popup">
        <div className="popup_container">
          <p>
            Chcete vytvořit opravnou zkoušku pro{' '}
            <span className="student_name"></span>
          </p>
          <div className="decision_row">
            <button id="yes_repair">
              <a href="/editExamp">ANO</a>
            </button>
            <button id="no_repair">NE</button>
          </div>
        </div>
      </div>
    </>
  );
};

export default DashboardTable;
