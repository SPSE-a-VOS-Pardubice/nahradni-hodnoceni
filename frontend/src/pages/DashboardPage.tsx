import {useEffect, useState} from 'react';
import './Dashboard.css';

import DashboardStats from '../components/home/DashboardStats';
import FilterOptions from '../components/home/FilterOptions';
import DashboardTable from '../components/home/DashboardTable';
import {fetchDashboardStats, fetchExams} from '../ApiClient';
import Exam from '../models/data/Exam';
import DashboardStatsData from '../models/DashboardStatsData';
import FilterParams from '../models/FilterParams';
import Import from '../components/import/Import';
import ImportPhase from '../models/ImportPhase';
import DashboardSearch from '../components/home/DashboardSearch';

const DashboardPage = () => {

  const [stats, setStats] = useState<DashboardStatsData | null>(null);
  const [exams, setExams] = useState<Exam[]>([]);
  const [filterParams, setFilterParams] = useState(new FilterParams());
  const [importPhase, setImportPhase] = useState<ImportPhase>({
    phase: '1_UPLOAD',
  });

  useEffect(() => {
    (async () => {
      setStats(await fetchDashboardStats());
    })();
  }, [exams]);

  useEffect(() => {
    fetchExams(filterParams).then(exams => grouping(exams)).then(setExams);
  }, [filterParams]);

  useEffect(() => {
    if (importPhase.phase === '5_SUCCESS') { fetchExams(filterParams).then(exams => grouping(exams)).then(setExams); }
  }, [importPhase]);

  function onExamUpdate(newExam: Exam) {
    const newExams = Object.assign([], exams);
    Object.assign(
            exams.find((exam) => exam.id === newExam.id)!,
            newExam,
        );
    setExams(grouping(newExams));
  }

  function handleSearch(text: string) {
    const newFilterParams = {...filterParams};
    newFilterParams.text = text;
    setTimeout(setFilterParams, 0, newFilterParams);
  }

  function grouping(exams: Exam[]) {
    return exams.sort((a,b) => {
        return (a.student._class.year - b.student._class.year) * 1000 +
                (a.student._class.label.localeCompare(b.student._class.label)) * 100 + 
                (a.student.surname.localeCompare(b.student.surname)) * 10 + 
                (a.student.name.localeCompare(b.student.name));
      })
    }

  return (
        <>
            <DashboardStats stats={stats} />
            <DashboardSearch onSubmit={handleSearch} />

            <div className="num_of_records_part">
                <p>Zobrazeno {exams.length} záznamů</p>
            </div>

            <FilterOptions params={filterParams} setParams={setFilterParams} />
            <DashboardTable exams={exams} onExamUpdate={onExamUpdate} />

            <Import phase={importPhase} setPhase={setImportPhase} />
        </>
  );
};

export default DashboardPage;
