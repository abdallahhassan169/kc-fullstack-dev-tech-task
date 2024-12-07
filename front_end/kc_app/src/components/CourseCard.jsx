import React from "react";
import { Card } from "react-bootstrap";

const CourseCard = ({ title, description, category }) => {
  return (
    <Card className="mb-4 shadow-sm">
      <div className="position-relative">
        <Card.Img
          variant="top"
          src="https://via.placeholder.com/150"
          alt="Course Thumbnail"
        />
        <span className="badge top-right">{category}</span>
      </div>
      <Card.Body>
        <Card.Title className="text-truncate">{title}</Card.Title>
        <Card.Text className="description text-truncate">
          {description}
        </Card.Text>
        <Card.Text className="text-muted small">{category}</Card.Text>
      </Card.Body>
    </Card>
  );
};

export default CourseCard;
