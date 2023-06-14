import Exam from '../../models/data/Exam';
import './DashboardTable.css';
import DashboardTableItem from './DashboardTableItem';

const DashboardTable = (props: {
    exams: Exam[],
    // eslint-disable-next-line no-unused-vars
    onExamUpdate: (newExam: Exam) => void
}) => {
  return (
        <>
            <table className="dashboard">
                <tbody>
                    {props.exams.map((exam, i) => (
                        <DashboardTableItem key={i} exam={exam} onExamUpdate={props.onExamUpdate} />
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
  );
};

export default DashboardTable;
