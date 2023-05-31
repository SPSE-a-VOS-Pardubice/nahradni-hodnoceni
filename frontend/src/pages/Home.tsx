import React from 'react'
import './Home.css'

import ExamsStats from '../components/home/ExamsStats'
import HalfYearConfig from '../components/home/HalfYearConfig'
import HeaderAside from '../components/HeaderAside'
import HeaderSearch from '../components/home/HeaderSearch'
import TableOptions from '../components/home/TableOptions'
import DashBoard from '../components/home/DashBoard'

const Home = () => {



    return (
        <>
            <header>
                <HeaderAside useButton={true} />

                <div className="header_main">
                    <div className="app_main_info_part">
                        <div className="app_data_part">
                            <h1>Náhradní hodnocení a opravné zkoušky</h1>

                            <ExamsStats />
                        </div>

                        <HalfYearConfig />
                    </div>
                    <div className="progress_bar_part">
                        <div className="progress_bar">
                            <div className="progress_succeed"></div>
                            <div className="progress_failed"></div>
                            <div className="progress_unmarked"></div>
                        </div>

                        <button id="progress_graph_btn">
                            <a href="/graf">
                                <i className="fa-solid fa-chart-simple"></i>
                            </a>
                        </button>
                    </div>

                    <HeaderSearch />

                    <div className="num_of_records_part">
                        <p>Zobrazeno 136 záznamů</p>
                    </div>

                </div>
            </header>
            <main>

                <TableOptions />
                <DashBoard />
            </main>
        </>
    )
}

export default Home