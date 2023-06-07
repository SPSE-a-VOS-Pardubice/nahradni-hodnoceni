import { useEffect, useState } from 'react'
import './Dashboard.css'

import DashboardStats from '../components/home/DashboardStatsComponent'
import FilterOptions from '../components/home/FilterOptionsComponent'
import DashboardTable from '../components/home/DashboardTableComponent'
import { fetchDashboardStats, fetchExams } from '../ApiClient'
import Exam from '../models/data/Exam'
import DashboardStatsData from '../models/DashboardStatsData'
import FilterParams from '../models/FilterParams'
import Import from '../components/import/Import'

const DashboardPage = () => {

    const [stats, setStats] = useState<DashboardStatsData | null>(null);
    const [page, setPage] = useState<number>(0);
    const [exams, setExams] = useState<Exam[]>([]);
    const [filterParams, setFilterParams] = useState(new FilterParams());
    const [importOpen, setImportOpen] = useState<boolean>(false);

    useEffect(() => {
        (async () => {
            setStats(await fetchDashboardStats());
        })();
    }, [exams]);

    useEffect(() => {
        (async () => {
            const exams = await fetchExams(filterParams, page);
            if (exams.length === 0)
                setImportOpen(true);
            setExams(exams);
        })();
    }, [page, filterParams]);

    function onExamUpdate(newExam: Exam) {
        const newExams = Object.assign([], exams);
        Object.assign(
            exams.find(exam => exam.id === newExam.id)!,
            newExam
        );
        setExams(newExams);
    }

    function handleSearch(text: string) {
        console.log(`User searched for: ${text}`);
    }

    return (
        <>
            <DashboardStats stats={stats} />
            {/* <DashboardSearch onSubmit={handleSearch} /> */}

            {/* <div className="num_of_records_part">
                <p>Zobrazeno 136 záznamů</p>
            </div> */}

            <FilterOptions params={filterParams} setParams={setFilterParams} />
            <DashboardTable exams={exams} onExamUpdate={onExamUpdate} />

            <Import open={importOpen} onFinish={() => setImportOpen(false)} />
        </>
    )
}

export default DashboardPage
