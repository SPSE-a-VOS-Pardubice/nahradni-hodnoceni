import {useContext, useState} from 'react';
import './DashboardTable.css';
import DashboardTableItem from './DashboardTableItem';
import {ExamsContext} from '../../contexts/ExamsContext';
import DashboardSearch from './DashboardSearch';
import {ExamDisplayRestrictions} from '../../models/ExamDisplayRestrictions';
import {isExamNH} from '../../services/ExamService';
import Exam from '../../models/data/Exam';
import FilterOptions from './SearchOptions';

const applyFilter = (examDisplayRestrictions: ExamDisplayRestrictions, exams: Exam[]) => {
  return exams.filter(exam => {
    // filter.mark
    if (examDisplayRestrictions.filter.mark !== undefined && examDisplayRestrictions.filter.mark !== exam.finalMark) {
      return false;
    }
    // filter.status
    if (examDisplayRestrictions.filter.status === 'finished' && exam.finalMark === null) {
      return false;
    }
    if (examDisplayRestrictions.filter.status === 'unfinished' && exam.finalMark !== null) {
      return false;
    }
    // filter.success
    if (examDisplayRestrictions.filter.success === 'successful' && (exam.finalMark === null || exam.finalMark === '5')) {
      return false;
    }
    if (examDisplayRestrictions.filter.success === 'failed' && exam.finalMark !== '5') {
      return false;
    }
    // filter.type
    if (examDisplayRestrictions.filter.type === 'nahradni_hodnoceni' && !isExamNH(exam)) {
      return false;
    }
    if (examDisplayRestrictions.filter.type === 'opravna_zkouska' && isExamNH(exam)) {
      return false;
    }

    return true;
  });
};

const applySearch = (examDisplayRestrictions: ExamDisplayRestrictions, exams: Exam[]) => {
  return exams.filter(exam => {
    return true; // TODO search
  });
};

const DashboardTable = () => {
  const examsContext = useContext(ExamsContext);

  const [examDisplayRestrictions, setExamDisplayRestrictions] = useState(new ExamDisplayRestrictions());

  // existence of exams is already checked in DashboardPage
  if (examsContext.id !== 'SUCCESS') {
    return <></>;
  }
  let exams = examsContext.content.data;

  exams = applyFilter(examDisplayRestrictions, exams);
  exams = applySearch(examDisplayRestrictions, exams);
  // TODO sort
  // TODO reverse

  let renderedExams;
  if (examDisplayRestrictions.groupBy === undefined) {
    renderedExams = (
      <table className="dashboard">
        <tbody>
          {exams.map((exam, i) => (
            <DashboardTableItem key={i} exam={exam} />
          ))}
        </tbody>
      </table>
    );
  } else {
    // TODO group by
    renderedExams = (
      <ul>

      </ul>
    );
  }

  const setSearchFor = (newSearchFor: string) => {
    const newExamDisplayRestrictions = structuredClone(examDisplayRestrictions);
    newExamDisplayRestrictions.searchFor = newSearchFor;
    setExamDisplayRestrictions(newExamDisplayRestrictions);
  };

  return (
    <>
      <DashboardSearch searchFor={examDisplayRestrictions.searchFor} setSearchFor={setSearchFor} />
      <FilterOptions restrictions={examDisplayRestrictions} setRestrictions={setExamDisplayRestrictions} />

      <div className="num_of_records_part">
        <p>Zobrazeno {exams.length} záznamů</p>
      </div>

      {renderedExams}

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
