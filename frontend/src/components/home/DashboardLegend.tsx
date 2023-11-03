import {useContext, useEffect, useState} from 'react';
import './DashboardLegend.css';
import {Period, PeriodContext} from '../../contexts/PeriodContext';
import StatusWrapper from '../../models/StatusWrapper';
import {fetchOldestYear} from '../../services/APIService';

const formatPeriod = (period: Period) => {
  return `${period.year}/${period.year + 1} - ${period.period}. pololetí`; // TODO use intl instead
};

const DashboardLegend = () => {
  const period = useContext(PeriodContext).data;

  const [oldestYear, setOldestYear] = useState<StatusWrapper<number | null>>({id: 'FETCHING'});

  console.log(oldestYear);

  useEffect(() => {
    setOldestYear({id: 'FETCHING'});
    fetchOldestYear()
      .then(newData => setOldestYear({id: 'SUCCESS', content: newData}))
      .catch(reason => setOldestYear({id: 'FAILED', message: reason}));
  }, []);

  return (
    <div className="half_year_config_part">
      <button className="select" name="half_year_config_select" id="half_year_config_select">
          <span>{formatPeriod(period)}<i className="fa-solid fa-angle-down"></i></span>
          <div className="dropdown">
            <option value="2021">2021/2022 - 2. pololetí</option>
            <option value="2022">2022/2023 - 1. pololetí</option>
            <option value="2022">2022/2023 - 2. pololetí</option>
          </div>
      </button>

      <section className="app_colors_meaning col p-0">
        <article id="succeeded" className="color_meaning_art">
          <p>Úspěšně</p>
          <div className="color_rect"></div>
        </article>
        <article id="failed" className="color_meaning_art">
          <p>Neúspěšně</p>
          <div className="color_rect"></div>
        </article>
        <article id="unmarked" className="color_meaning_art">
          <p>Nehodnoceno</p>
          <div className="color_rect"></div>
        </article>
      </section>
    </div>
  );
};

export default DashboardLegend;
