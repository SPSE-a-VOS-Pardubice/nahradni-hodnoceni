import {useContext} from 'react';
import './DashboardTableItem.css';
import Classroom from '../../models/data/Classroom';
import {ClassroomsContext} from '../../contexts/ClassroomsContext';
import {uploadData} from '../../services/APIService';

const ClassroomTableItem = (props: {
    classroom: Classroom
}) => {
  const classroomsContext = useContext(ClassroomsContext);

  // existence of classrooms is already checked in ClassroomsPage
  if (classroomsContext.id !== 'SUCCESS') {
    return <></>;
  }

  console.log(props.classroom);

  return (
    <tr className="dashboard_table_item">
      <td>
        <span className="main_data">{props.classroom.label}</span>
        {props.classroom.traits.map(trait => {
          async function handleClick() {
            const newClassroom = structuredClone(props.classroom);
            newClassroom.traits = newClassroom.traits.filter(t => t.id !== trait.id);
            await uploadData(newClassroom, 'classroom');
          }

          return (
            <span onClick={handleClick}>{trait.name}</span>
          );
        })}
      </td>
    </tr>
  );
};

export default ClassroomTableItem;
