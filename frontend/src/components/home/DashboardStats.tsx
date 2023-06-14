import './DashboardStats.css'
import DashboardLegend from './DashboardLegend'
import DashboardStatsData from '../../models/DashboardStatsData';

const DashboardStats = (props: {
    stats: DashboardStatsData | null
}) => {

    let finished = null;
    if (props.stats !== null)
        finished = (props.stats.finishedNH + props.stats.finishedOZ) / (props.stats.totalNH + props.stats.totalOZ) * 100;

    return (
        <div className="app_main">
            <div className="app_main_info_part">
                <div className="app_data_part">
                    <h1>Náhradní hodnocení a opravné zkoušky</h1>

                    <section className="app_main_data_sec p-0">
                        {props.stats === null ? (
                            <>{/* TODO zobrazit nejakou formu nacitani / erroru ? */}</>
                        ) : (
                            <>
                                <DashboardSingleStat name='Dokončeno' value={`${finished!.toFixed(0)}%`} />
                                <DashboardSingleStat name='Dokončeno NH' value={`${props.stats.finishedNH}/${props.stats.totalNH}`} />
                                <DashboardSingleStat name='Dokončeno OZ' value={`${props.stats.finishedOZ}/${props.stats.totalOZ}`} />
                                <DashboardSingleStat name='Žáků prospělo' value={props.stats.succeeded.toString()} />
                            </>
                        )}
                    </section>
                </div>

                <DashboardLegend />
            </div>
            <div className="progress_bar_part">
                <div className="progress_bar">
                    <div className="progress_succeeded" style={{"flex": props.stats?.succeeded}}></div>
                    <div className="progress_failed" style={{"flex": props.stats?.failed}}></div>
                    <div className="progress_unmarked" style={{"flex": props.stats?.unmarked}}></div>
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

const DashboardSingleStat = (props: {
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

export default DashboardStats
