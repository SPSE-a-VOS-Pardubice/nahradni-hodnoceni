import React, { useEffect, useState } from 'react'
import './DashboardStats.css'
import DashboardLegendComponent from './DashboardLegendComponent'
import { getDashboardStats } from '../../ApiClient';
import DashboardStats from '../../models/DashboardStats';

const DashboardStatsComponent = () => {

    const [stats, setStats] = useState<{
        stats: DashboardStats,
        finished: number
    } | null>(null);

    useEffect(() => {
        (async () => {
            const stats = await getDashboardStats();
            setStats({
                stats,
                finished: (stats.finishedNH + stats.finishedOZ) / (stats.totalNH + stats.totalOZ) * 100
            });
        })();
    }, []);

    return (
        <div className="app_main">
            <div className="app_main_info_part">
                <div className="app_data_part">
                    <h1>Náhradní hodnocení a opravné zkoušky</h1>

                    <section className="app_main_data_sec p-0">
                        {stats === null ? (
                            <>{/* TODO zobrazit nejakou formu nacitani / erroru ? */}</>
                        ) : (
                            <>
                                <DashboardSingleStatComponent name='Dokončeno' value={`${stats.finished.toFixed(0)}%`} />
                                <DashboardSingleStatComponent name='Dokončeno NH' value={`${stats.stats.finishedNH}/${stats.stats.totalNH}`} />
                                <DashboardSingleStatComponent name='Dokončeno OZ' value={`${stats.stats.finishedOZ}/${stats.stats.totalOZ}`} />
                                <DashboardSingleStatComponent name='Žáků prospělo' value='TODO' />
                            </>
                        )}
                    </section>
                </div>

                <DashboardLegendComponent />
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
        </div>
    )
}

const DashboardSingleStatComponent = (props: {
    value: string,
    name: string
}) => {

    return (
        <article className="app_main_data_art">
            <h2>{props.value}</h2>
            <h3>{props.name}</h3>
        </article>
    );
}

export default DashboardStatsComponent
