import React from 'react'
import './ExamsStats.css'

const ExamsStats = () => {
    return (
        <section className="app_main_data_sec p-0">
            <article id="finished" className="app_main_data_art">
                <h2>35%</h2>
                <h3>Dokončeno</h3>
            </article>
            <article id="finished_NH" className="app_main_data_art">
                <h2>120/256</h2>
                <h3>Dokončeno NH</h3>
            </article>
            <article id="finished_OZ" className="app_main_data_art">
                <h2>10/52</h2>
                <h3>Dokončeno OZ</h3>
            </article>
            <article id="students_benefited" className="app_main_data_art">
                <h2>20/120</h2>
                <h3>Žáků prospělo</h3>
            </article>
        </section>
    )
}

export default ExamsStats
