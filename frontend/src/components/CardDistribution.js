import React, { useState, useCallback, useMemo } from 'react';
import axios from 'axios';

const CardDistribution = () => {
    const [numPeople, setNumPeople] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    const [results, setResults] = useState([]);
    const [warning, setWarning] = useState('');

    const API_URL = 'http://localhost:8765/cards/distribute';

    const validateInput = (value) => {
        // Check if input contains anything other than numbers
        if (!/^\d*$/.test(value)) {
            return { isValid: false, message: 'Input value does not exist or value is invalid' };
        }
        // Check for zero, negative numbers, or leading zeros
        if (value === '0' || value <= 0 || /^0\d+/.test(value)) {
            return { isValid: false, message: 'Input value does not exist or value is invalid' };
        }
        // Check if number is greater than 52 (but still allow processing)
        if (parseInt(value) > 52) {
            return { 
                isValid: true, 
                message: '', 
                warning: 'Total 52 cards, Person 53, and the next one doesn\'t hold any cards.' 
            };
        }
        return { isValid: true, message: '', warning: '' };
    };

    const distributeCards = useCallback(async () => {
        setError('');
        setResults([]);
        setLoading(true);

        const validation = validateInput(numPeople);
        if (!validation.isValid) {
            setError(validation.message);
            setLoading(false);
            return;
        }

        setWarning(validation.warning || '');

        try {
            await new Promise(resolve => setTimeout(resolve, 1000));

            const response = await axios.post(`${API_URL}/distribute`, {
                numPeople: parseInt(numPeople)
            }, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            if (response.data?.data) {
                // Create array with null values for persons beyond 53
                const distributedCards = response.data.data.split('\n');
                const totalPeople = parseInt(numPeople);
                const results = Array(totalPeople).fill(null);
                
                // Fill in the actual card distributions for persons 1-52
                distributedCards.forEach((hand, index) => {
                    if (index < 52) {
                        results[index] = hand;
                    }
                });
                
                setResults(results);
            } else {
                setError('Irregularity occurred');
            }
        } catch (err) {
            setError('Irregularity occurred');
        } finally {
            setLoading(false);
        }
    }, [numPeople]);

    const handleInputChange = useCallback((e) => {
        const value = e.target.value;
        setNumPeople(value);

        const validation = validateInput(value);
        if (!validation.isValid) {
            setError(validation.message);
            setWarning('');
        } else {
            setError('');
            setWarning(validation.warning || '');
        }
    }, []);

    React.useEffect(() => {
        const handleKeyPress = (e) => {
            if (e.key === 'Enter') {
                distributeCards();
            }
        };
        
        document.addEventListener('keypress', handleKeyPress);
        return () => {
            document.removeEventListener('keypress', handleKeyPress);
        };
    }, [distributeCards]);

    const isDistributeDisabled = useMemo(() => {
        const validation = validateInput(numPeople);
        return loading || !validation.isValid;
    }, [loading, numPeople]);

    return (
        <div className="card-distribution">
            <div className="container">
                <h1>Playing Cards</h1>

                <div className="input-section">
                    <div className="input-group">
                        <input
                            type="text"
                            value={numPeople}
                            onChange={handleInputChange}
                            onKeyPress={(e) => {
                                if (e.key === 'Enter') {
                                    distributeCards();
                                }
                            }}
                            placeholder="Enter number of people"
                            className="number-input"
                            disabled={loading}
                        />
                        <button 
                            onClick={distributeCards}
                            disabled={isDistributeDisabled}
                            className="distribute-button"
                        >
                            {loading ? 'Processing...' : 'Distribute Cards'}
                        </button>
                    </div>
                </div>

                {error && <div className="error-message">{error}</div>}
                {warning && <div className="warning-message">{warning}</div>}

                {loading && (
                    <div className="loading-spinner">
                        <div className="spinner"></div>
                        <p>Processing...</p>
                    </div>
                )}

                <div className="result-section">
                    {results.map((hand, index) => (
                        <div key={index} className="hand">
                            <span className="hand-number">Person {index + 1}</span>
                            <span className="hand-cards">
                                {hand === null ? 'null' : hand}
                            </span>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default CardDistribution;
