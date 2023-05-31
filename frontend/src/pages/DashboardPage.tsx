import React from 'react'
import './Dashboard.css'

import DashboardStatsComponent from '../components/home/DashboardStatsComponent'
import TableOptions from '../components/home/TableOptions'
import DashboardComponent from '../components/home/DashboardComponent'
import DashboardSearchComponent from '../components/home/DashboardSearchComponent'

const DashboardPage = () => {

    function handleSearch(text: string) {
        console.log(`User searched for: ${text}`);
    }

    return (
        <>
            <DashboardStatsComponent />
            <DashboardSearchComponent onSubmit={handleSearch} />

            {/* <div className="num_of_records_part">
                <p>Zobrazeno 136 záznamů</p>
            </div> */}

            <TableOptions />
            <DashboardComponent />
        </>
    )
}

export default DashboardPage
