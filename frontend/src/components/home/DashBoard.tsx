import React from 'react'
import { useEffect, useState } from 'react'
import './DashBoard'

const DashBoard = () => {

    const [rows, setRows] = useState([])

    useEffect(() => {
        // TODO až bude api
        /*
        fetch('asasddas')
        .then(res => {
            if (res.status === 200) 
                return res.json()
            else return []
        })
        .then(data => {
            setRows(data.map(row => row))
        })
        */
    })

    return (
        <>
            <table className="dashboard">
                <tbody>
                    {rows}
                </tbody>
            </table>

            <div className="delete_popup popup">
                <div className="popup_container">
                    <p>Opravdu si přejete smazat náhradní hodnocení <span className="student_name"></span></p>
                    <div className="decision_row">
                        <button id="yes_delete">ANO</button>
                        <button id="no_delete">NE</button>
                    </div>
                </div>
            </div>

            <div className="repair_popup popup">
                <div className="popup_container">
                    <p>Chcete vytvořit opravnou zkoušku pro <span className="student_name"></span></p>
                    <div className="decision_row">
                        <button id="yes_repair">
                            <a href="/editExamp">
                                ANO
                            </a>
                        </button>
                        <button id="no_repair">NE</button>
                    </div>
                </div>
            </div>
        </>
    )
}

export default DashBoard
