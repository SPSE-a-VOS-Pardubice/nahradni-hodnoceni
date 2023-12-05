import {useContext} from 'react';
import './DashboardTable.css';
import {ClassroomsContext} from '../../contexts/ClassroomsContext';
import ClassroomTableItem from './ClassroomTableItem';

const ClassroomTable = () => {
  const classroomsContext = useContext(ClassroomsContext);

  // existence of classrooms is already checked in ClassroomsPage
  if (classroomsContext.id !== 'SUCCESS') {
    return <></>;
  }
  const classrooms = classroomsContext.content.data;

  return (
    <table className="dashboard_table">
      <tbody>
        {classrooms.map((classroom, i) => (
          <ClassroomTableItem key={i} classroom={classroom} />
        ))}
      </tbody>
    </table>
  );
};

export default ClassroomTable;
