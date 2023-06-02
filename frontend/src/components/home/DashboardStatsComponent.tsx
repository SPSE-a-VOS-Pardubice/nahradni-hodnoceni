import './DashboardStats.css'
import DashboardLegendComponent from './DashboardLegendComponent'
import DashboardStats from '../../models/DashboardStats';

const DashboardStatsComponent = (props: {
    stats: DashboardStats | null
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
                                <DashboardSingleStatComponent name='Dokončeno' value={`${finished!.toFixed(0)}%`} />
                                <DashboardSingleStatComponent name='Dokončeno NH' value={`${props.stats.finishedNH}/${props.stats.totalNH}`} />
                                <DashboardSingleStatComponent name='Dokončeno OZ' value={`${props.stats.finishedOZ}/${props.stats.totalOZ}`} />
                                <DashboardSingleStatComponent name='Žáků prospělo' value={props.stats.succeeded.toString()} />
                            </>
                        )}
                    </section>
                </div>

                <DashboardLegendComponent />
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
