import {useContext, useEffect, useState} from 'react';
import './Dashboard.css';

import ImportPhase from '../models/ImportPhase';
import {uploadCsvFile, uploadData} from '../services/APIService';
import ImportDropzone from '../components/import/ImportDropzone';
import {FormattedMessage} from 'react-intl';
import Teacher from '../models/data/Teacher';
import Subject from '../models/data/Subject';
import {PeriodContext} from '../contexts/PeriodContext';

const ImportPage = () => {
  const {data: period, setData: setPeriod} = useContext(PeriodContext);
  const [phase, setPhase] = useState<ImportPhase>({
    id: '1_UPLOAD',
  });

  useEffect(() => {
    if (phase.id === '2_UPLOADING') {
      uploadCsvFile(phase.data, phase.period).then((response) => {
        if (phase.id !== '2_UPLOADING') {
          throw new Error(
            'Invalid state: import phase changed while uploading data.',
          );
        }

        if (response === '') {
          setPhase({
            id: '5_SUCCESS',
          });
        } else {
          setPhase({
            id: '3_MISSING_EXAMINERS',
            data: phase.data,
            period: phase.period,
            missingExaminers: response.missingExaminers,
            missingSubjects: response.missingSubjects,
          });
        }
      });
    } else if (phase.id === '3_MISSING_EXAMINERS') {
      if (phase.missingExaminers.length === 0) {
        setPhase({
          id: '4_MISSING_SUBJECTS',
          data: phase.data,
          period: phase.period,
          missingExaminers: phase.missingExaminers,
          missingSubjects: phase.missingSubjects,
        });
      }
    } else if (phase.id === '4_MISSING_SUBJECTS') {
      if (phase.missingSubjects.length === 0) {
        setPhase({
          id: '2_UPLOADING',
          data: phase.data,
          period: phase.period,
        });
      }
    }
  }, [phase]);


  if (phase.id === '1_UPLOAD') {
    const handleUpload = (data: ArrayBuffer) => {
      setPhase({
        id: '2_UPLOADING',
        data,
        period,
      });
    };

    return (
      <>
        <button className="select">
          <span>{period.period}. pololetí</span>

          <div className="dropdown">
            <option onClick={() => setPeriod({year: period.year, period: 1})}>1</option>
            <option onClick={() => setPeriod({year: period.year, period: 2})}>2</option>
          </div>
        </button>
        <ImportDropzone onDrop={handleUpload}>
          <h3>
            <FormattedMessage id="import.drop" />
          </h3>
        </ImportDropzone>
      </>
    );
  }

  if (phase.id === '2_UPLOADING') {
    return (
      <h3>Nahrávání...</h3>
    );
  }

  if (phase.id === '3_MISSING_EXAMINERS') {
    const examinerDetails = phase.missingExaminers[0];

    const handleSubmit = async () => {
      const examinerPrefixEl = document.getElementById('import_examiner_prefix') as HTMLInputElement;
      const examinerSuffixEl = document.getElementById('import_examiner_suffix') as HTMLInputElement;

      await uploadData(
        {
          name: examinerDetails.name,
          surname: examinerDetails.surname,
          prefix: examinerPrefixEl.value,
          suffix: examinerSuffixEl.value,
        } as Teacher,
        'teacher',
      );

      if (phase.id !== '3_MISSING_EXAMINERS') {
        throw new Error(
          'Invalid state: import phase changed while uploading data.',
        );
      }

      examinerPrefixEl.value = '';
      examinerSuffixEl.value = '';

      const newPhase = {...phase};
      newPhase.missingExaminers.shift();
      setPhase(newPhase);
    };

    return (
      <div>
        <h3>
          Prosím vyplňte chybějící údaje pro učitele {examinerDetails.name} {examinerDetails.surname}
        </h3>
        <br />
        <br />
        <label htmlFor="import_examiner_prefix">Prefix</label>
        <input id="import_examiner_prefix" type="text"></input>
        <br />
        <br />
        <label htmlFor="import_examiner_suffix">Suffix</label>
        <input id="import_examiner_suffix" type="text"></input>
        <button onClick={handleSubmit}>OK</button>
      </div>
    );
  }

  if (phase.id === '4_MISSING_SUBJECTS') {
    const subjectAbbreviation = phase.missingSubjects[0];

    const handleData = async () => {
      const subjectNameEl = document.getElementById('subject_name') as HTMLInputElement;

      await uploadData(
        {
          name: subjectNameEl.value,
          abbreviation: subjectAbbreviation,
        } as Subject,
        'subject',
      );

      if (phase.id !== '4_MISSING_SUBJECTS') {
        throw new Error(
          'Invalid state: import phase changed while uploading data.',
        );
      }

      subjectNameEl.value = '';

      const newPhase = {...phase};
      newPhase.missingSubjects.shift();
      setPhase(newPhase);
    };

    return (
      <form onSubmit={handleData}>
        <h3>
          Prosím vyplňte chybějící údaje pro předmět se zkratkou:{' '}
          {subjectAbbreviation}
        </h3>
        <br />
        <br />
        <label htmlFor="subject_name">Název předmětu</label>
        <input id="subject_name" type="text"></input>
        <button onClick={handleData}>OK</button>
      </form>
    );
  }

  if (phase.id === '5_SUCCESS') {
    return (
      <div>
        <h3>Import dat proběhl úspěšně</h3>
        <button onClick={() => setPhase({id: '1_UPLOAD'})}>OK</button>
      </div>
    );
  }

  return <></>;
};

export default ImportPage;
