import {useContext, useState} from 'react';
import './DashboardTable.css';
import DashboardTableItem from './DashboardTableItem';
import {ExamsContext} from '../../contexts/ExamsContext';
import DashboardSearch from './DashboardSearch';
import {ExamDisplayRestrictions} from '../../models/ExamDisplayRestrictions';
import {isExamNH} from '../../services/ExamService';
import Exam from '../../models/data/Exam';
import FilterOptions from './FilterOptions';
import { Period, PeriodContext } from '../../contexts/PeriodContext';
import { formatClassRelativeToPeriod } from '../../services/_ClassService';

function applyFilter(examDisplayRestrictions: ExamDisplayRestrictions, exams: Exam[]) {
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
}

// https://stackoverflow.com/a/37511463/14693511
function normalizeToken(token: string): string {
  return token
    .normalize('NFD')
    .replace(/\p{Diacritic}/gu, '')
    .toLowerCase();
}

function applySearch(period: Period, examDisplayRestrictions: ExamDisplayRestrictions, exams: Exam[]): Exam[] {
  if (examDisplayRestrictions.searchFor === '') {
    return exams;
  }

  const tokens = examDisplayRestrictions.searchFor.split(' ').map(normalizeToken);
  console.log('searching for tokens:', tokens);

  return exams.filter(
    exam => tokens.every(
      token => {
        if (normalizeToken(exam.student.name).includes(token)) {
          return true;
        }
        if (normalizeToken(exam.student.surname).includes(token)) {
          return true;
        }
        if (normalizeToken(formatClassRelativeToPeriod(exam.student._class, period)).includes(token)) {
          return true;
        }
        if (exam.classroom && normalizeToken(exam.classroom.label).includes(token)) {
          return true;
        }
        // TODO search by date and time

        return false;
      },
    ),
);
}

const DashboardTable = () => {
  const examsContext = useContext(ExamsContext);
  const period = useContext(PeriodContext).data;

  const [examDisplayRestrictions, setExamDisplayRestrictions] = useState(new ExamDisplayRestrictions());

  // existence of exams is already checked in DashboardPage
  if (examsContext.id !== 'SUCCESS') {
    return <></>;
  }
  let exams = examsContext.content.data;

  exams = applyFilter(examDisplayRestrictions, exams);
  exams = applySearch(period, examDisplayRestrictions, exams);
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
