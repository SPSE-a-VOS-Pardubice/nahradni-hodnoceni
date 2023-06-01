import React, { useEffect, useState } from 'react'
import './Dashboard.css'

import DashboardStatsComponent from '../components/home/DashboardStatsComponent'
import TableOptions from '../components/home/TableOptions'
import DashboardTableComponent from '../components/home/DashboardTableComponent'
import DashboardSearchComponent from '../components/home/DashboardSearchComponent'
import { fetchDashboardStats, fetchExams } from '../ApiClient'
import Exam from '../models/Exam'
import DashboardStats from '../models/DashboardStats'

const DashboardPage = () => {

    const [stats, setStats] = useState<DashboardStats | null>(null);
    const [page, setPage] = useState<number>(0);
    const [exams, setExams] = useState<Exam[]>([]);

    useEffect(() => {
        (async () => {
            setStats(await fetchDashboardStats());
        })();
    }, [exams]);

    useEffect(() => {
        (async () => {
            const exams = await fetchExams(page);
            setExams(exams);
        })();
    }, [page]);

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
            <DashboardStatsComponent stats={stats} />
            <DashboardSearchComponent onSubmit={handleSearch} />

            {/* <div className="num_of_records_part">
                <p>Zobrazeno 136 záznamů</p>
            </div> */}

            <TableOptions />
            <DashboardTableComponent exams={exams} onExamUpdate={onExamUpdate} />
        </>
    )
}

export default DashboardPage
