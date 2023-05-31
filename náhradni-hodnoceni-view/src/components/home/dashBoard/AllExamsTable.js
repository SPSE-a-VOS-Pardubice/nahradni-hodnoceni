import { useEffect, useState } from 'react'
import './AllExamsTable.css'

const AllExamsTable = () => {

    const [rows, setRows] = useState([])

    useEffect(() => {
        // TODO získej všechny zkoušky pro toho studenta na kterého uživatel ukazuje a udělej z nich DashBoardRow s nastavením  showAllExams = false
        setRows([])
    }) 

    
    return (
        <table class="all_exams">
            {rows}
        </table>
    )
}

export default AllExamsTable