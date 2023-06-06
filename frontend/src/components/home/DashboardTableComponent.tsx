import './DashboardTable.css'
import DashboardTableItemComponent from './DashboardTableItemComponent'
import Exam from '../../models/Exam'

const DashboardTableComponent = (props: {
    exams: Exam[],
    onExamUpdate: (newExam: Exam) => void
}) => {
    return (
        <>
            <table className="dashboard">
                <tbody>
                    {props.exams.map((exam, i) => (
                        <DashboardTableItemComponent key={i} exam={exam} onExamUpdate={props.onExamUpdate} />
                    ))}
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

export default DashboardTableComponent
