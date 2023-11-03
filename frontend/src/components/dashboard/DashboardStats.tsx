import './DashboardStats.css';

import DashboardLegend from './DashboardLegend';
import {useContext} from 'react';
import {ExamsContext} from '../../contexts/ExamsContext';
import {isExamNH} from '../../services/ExamService';

const DashboardSingleStat = (props: { value: string; name: string }) => {
  return (
    <article className="app_main_data_art">
      <h2>{props.value}</h2>
      <h3>{props.name}</h3>
    </article>
  );
};

const DashboardStats = () => {
  const examsContext = useContext(ExamsContext);
  // existence of exams is already checked in DashboardPage
  if (examsContext.id !== 'SUCCESS') {
    return <></>;
  }
  const exams = examsContext.content.data;

  const totalNH = exams.filter((exam) => isExamNH(exam)).length;

  const finishedNH = exams.filter(
    (exam) => isExamNH(exam) && exam.finalMark,
  ).length;

  const totalOZ = exams.filter((exam) => !isExamNH(exam)).length;

  const finishedOZ = exams.filter(
    (exam) => !isExamNH(exam) && exam.finalMark,
  ).length;

  const successful = exams.filter(
    (exam) =>
      exam.finalMark !== null &&
      exam.finalMark !== '5' &&
      exam.finalMark !== 'N',
  ).length;

  const failed = exams.filter(
    (exam) => exam.finalMark === '5' || exam.finalMark === 'N',
  ).length;

  const unmarked = exams.filter((exam) => exam.finalMark === null).length;

  const finishedPercentage =
    ((finishedNH + finishedOZ) / (totalNH + totalOZ)) * 100;

  return (
    <div className="app_main">
      <div className="app_main_info_part">
        <div className="app_data_part">
          <h1>Náhradní hodnocení a opravné zkoušky</h1>

          <section className="app_main_data_sec p-0">
            <DashboardSingleStat
              name="Dokončeno"
              value={`${Number.isNaN(finishedPercentage) ? '0' : finishedPercentage.toFixed(0)}%`}
            />
            <DashboardSingleStat
              name="Dokončeno NH"
              value={`${finishedNH}/${totalNH}`}
            />
            <DashboardSingleStat
              name="Dokončeno OZ"
              value={`${finishedOZ}/${totalOZ}`}
            />
            <DashboardSingleStat
              name="Žáků prospělo"
              value={successful.toString()}
            />
          </section>
        </div>

        <DashboardLegend />
      </div>
      <div className="progress_bar_part">
        <div className="progress_bar">
          <div
            className="progress_succeeded"
            style={{flex: successful}}
          ></div>
          <div className="progress_failed" style={{flex: failed}}></div>
          <div className="progress_unmarked" style={{flex: unmarked}}></div>
        </div>

        <button id="progress_graph_btn">
          <a href="/graf">
            <i className="fa-solid fa-chart-simple"></i>
          </a>
        </button>
      </div>
    </div>
  );
};

export default DashboardStats;
